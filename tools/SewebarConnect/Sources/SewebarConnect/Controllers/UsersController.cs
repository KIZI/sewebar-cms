using System.Linq;
using System.Net;
using System.Web.Http;
using SewebarConnect.API;
using SewebarConnect.API.Requests.Users;
using SewebarConnect.API.Responses.Users;

namespace SewebarConnect.Controllers
{
	[APIErrorHandler]
	[Authorize]
	public class UsersController : ApiBaseController
	{
		/// <summary>
		/// Lists all user or specific user.
		/// </summary>
		/// <param name="username">User to describe.</param>
		/// <returns>List of user or specific user description.</returns>
		[Filters.NHibernateTransaction]
		public Response Get(string username)
		{
			if (string.IsNullOrEmpty(username) && this.User.IsInRole("admin"))
			{
				var users = this.Repository.FindAll<SewebarKey.User>();

				return new UsersResponse(users);
			}
			else if (this.User.Identity.Name == username || this.User.IsInRole("admin"))
			{
				var user = this.Repository.Query<SewebarKey.User>()
				               .FirstOrDefault(u => u.Username == username);

				if (user != null)
				{
					return new UserResponse(user);
				}

				return this.ThrowHttpReponseException<Response>(HttpStatusCode.NotFound);
			}

			return this.ThrowHttpReponseException<Response>(HttpStatusCode.Unauthorized);
		}

		/// <summary>
		/// Register user or database for existing user.
		/// </summary>
		/// <returns>Registered UserResponse.</returns>
		[Filters.NHibernateTransaction]
		[AllowAnonymous]
		public UserResponse Post()
		{
			var request = new UserRequest(this);
			
			var user = this.Repository.Query<SewebarKey.User>()
				.FirstOrDefault(u => u.Username == request.UserName && u.Password == request.Password);

			if (user == null)
			{
				user = request.GetUser();

				this.Repository.Add(user);
			}

			var database = request.GetDatabase(user);

			if (database != null)
			{
				user.Databases.Add(database);
			}

			this.Repository.Save(user);

			return new UserResponse(user);
		}

		/// <summary>
		/// Update user or change user's password.
		/// </summary>
		/// <returns>UserResponse.</returns>
		[Filters.NHibernateTransaction]
		public UserResponse Put()
		{
			var request = new UserRequest(this);
			var user = this.GetSewebarUser();

			if (user.Username == request.UserName)
			{
				// updating himself
				if (!string.IsNullOrEmpty(request.NewUserName))
				{
					user.Username = request.NewUserName;
				}

				if (!string.IsNullOrEmpty(request.NewPassword))
				{
					user.Password = request.NewPassword;
				}

				this.Repository.Save(user);

				return new UserResponse(user);
			}
			else if (this.User.IsInRole("admin"))
			{
				// updating by admin
				SewebarKey.User modified = this.Repository.Query<SewebarKey.User>()
									.FirstOrDefault(u => u.Username == request.UserName);

				return this.ThrowHttpReponseException<UserResponse>(
					"This feature is not yet implemented",
					HttpStatusCode.NotImplemented);
			}

			return this.ThrowHttpReponseException<UserResponse>(
				string.Format("User \"{0}\" not found or you are not auhtorized to modify him.", request.UserName),
				HttpStatusCode.NotFound);
		}

		/// <summary>
		/// Deletes existing user.
		/// </summary>
		/// <param name="username">Username of user to delete.</param>
		/// <returns>Response with message.</returns>
		[Filters.NHibernateTransaction]
		public Response Delete(string username)
		{
			if (this.User.Identity.Name == username || this.User.IsInRole("admin"))
			{
				SewebarKey.User user = this.Repository.Query<SewebarKey.User>()
				                           .FirstOrDefault(u => u.Username == username);

				if (user != null)
				{
					this.Repository.Remove(user);

					return new Response(string.Format("User \"{0}\" removed.", user.Username));
				}

				return this.ThrowHttpReponseException(
					string.Format("User \"{0}\" not found.", username),
					HttpStatusCode.NotFound);
			}

			return this.ThrowHttpReponseException(
				string.Format("User \"{0}\" not found or you are not auhtorized to delete him.", username),
				HttpStatusCode.NotFound);
		}
	}
}
