using System;
using System.Linq;
using System.Net;
using System.Web.Http;
using SewebarConnect.API;
using SewebarConnect.API.Requests.Users;
using SewebarConnect.API.Responses.Users;
using SewebarKey;

namespace SewebarConnect.Controllers
{
	[APIErrorHandler]
	[Authorize]
	public class UsersController : ApiBaseController
	{
		[Filters.NHibernateTransaction]
		[AllowAnonymous]
		public Response Get(string username)
		{
			if (string.IsNullOrEmpty(username))
			{
				var users = Repository.FindAll<SewebarKey.User>();

				return new UsersResponse(users);
			}

			if (this.User.Identity.Name == username || this.User.IsInRole("admin"))
			{
				var user = this.Repository.Query<User>().FirstOrDefault(u => u.Username == username);

				return new UserResponse(user);
			}

			throw new HttpResponseException(HttpStatusCode.Unauthorized);
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
			
			User user = this.Repository.Query<User>()
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
			// TODO: change password and username as admin
			var request = new UserRequest(this);
			var user = this.GetSewebarUser();

			if (user == null)
			{
				throw new HttpResponseException(HttpStatusCode.NotFound);
			}

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

		/// <summary>
		/// Deletes existing user.
		/// </summary>
		/// <param name="username">Username of user to delete.</param>
		/// <returns>Response with message.</returns>
		[Filters.NHibernateTransaction]
		public Response Delete(string username)
		{
			User user = this.GetSewebarUser();

			if (user != null && user.Username == username)
			{
				// deleting himself
				this.Repository.Remove(user);

				return new Response(string.Format("User \"{0}\" removed.", user.Username));
			}
			else if (this.User.IsInRole("admin"))
			{
				// deleting by admin
				User deletion = this.Repository.Query<User>()
				                    .FirstOrDefault(u => u.Username == username);

				if (deletion != null)
				{
					this.Repository.Remove(deletion);

					return new Response(string.Format("User \"{0}\" removed by admin.", deletion.Username));
				}
			}

			throw new HttpResponseException(HttpStatusCode.NotFound);
		}
	}
}
