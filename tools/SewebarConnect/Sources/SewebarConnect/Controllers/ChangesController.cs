using System;
using System.Linq;
using System.Web.Http;
using SewebarConnect.API;
using SewebarConnect.API.Requests.Users;
using SewebarConnect.API.Responses.Users;
using SewebarKey.Repositories;

namespace SewebarConnect.Controllers
{
	[APIErrorHandler]
	public class ChangesController : ApiBaseController
	{
		[Filters.NHibernateTransaction]
		[Authorize(Roles = "admin")]
		public Response Get(string username, string id)
		{
			var change = this.Repository.Query<SewebarKey.UserPendingUpdate>()
				.Where(p => p.User.Username == username);

			if (change.Any())
			{
				return new Response(string.Join(", ", change.Select(c => c.Id)));
			}

			return ThrowHttpReponseException("User has no pending changes.");
		}

		[Filters.NHibernateSession]
		public Response Post(string username)
		{
			var repo = this.Repository as NHibernateRepository;
			var request = new UserChangeRequest(this);

			if (repo == null)
			{
				return ThrowHttpReponseException("Repository is not NHibernateRepository.");
			}

			using (var transaction = repo.Session.BeginTransaction())
			{
				try
				{
					var user = repo.Query<SewebarKey.User>()
						.FirstOrDefault(u => u.Username == username);

					if (user != null)
					{
						var update = request.GetPendingUpdate(user);

						user.PendingUpdates.Add(update);
						repo.Save(user);
						transaction.Commit();

						return new UserUpdateResponse(update, request.EmailFrom);
					}

					transaction.Rollback();

					return ThrowHttpReponseException("User not found.");
				}
				catch
				{
					transaction.Rollback();
					throw;
				}
			}
		}

		[Filters.NHibernateTransaction]
		public Response Put(string username)
		{
			var request = new UserChangeCommitRequest();
			var change = this.Repository.Query<SewebarKey.UserPendingUpdate>().
				FirstOrDefault(c => c.Id == request.Id && c.User.Username == username);

			if (change != null)
			{
				var user = change.User;

				if (user.PendingUpdates.Count > 1)
				{
					// TODO: how to handle?
					return ThrowHttpReponseException("User has more pending updates.");
				}

				user.Username = change.NewUsername ?? user.Username;
				user.Password = change.NewPassword ?? user.Password;
				user.Email = change.NewEmail ?? user.Email;

				user.PendingUpdates.Remove(change);

				this.Repository.Remove(change);
				this.Repository.Save(user);

				return new Response("Change has been applied.");
			}

			return ThrowHttpReponseException("No such change found.");
		}
	}
}