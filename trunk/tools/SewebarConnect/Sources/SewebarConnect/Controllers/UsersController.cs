using System;
using System.Linq;
using System.Web.Http;
using SewebarConnect.API;
using SewebarConnect.API.Requests.Users;
using SewebarConnect.API.Responses.Users;
using SewebarKey;
using SewebarKey.Repositories;

namespace SewebarConnect.Controllers
{
	[APIErrorHandler]
	public class UsersController : ApiBaseController
	{
		private IRepository _repository;

		protected IRepository Repository 
		{
			get
			{
				return _repository ?? (_repository = new NHibernateRepository(MvcApplication.SessionFactory.GetCurrentSession()));
			}
		}

		public override IRepository UsersRepository
		{
			get { return this.Repository; }
		}

		[Filters.NHibernateTransaction]
		public UsersResponse Get()
		{
			var users = Repository.FindAll<SewebarKey.User>();

			return new UsersResponse(users);
		}

		/// <summary>
		/// Tries to find database by it identification.
		/// </summary>
		/// <param name="dbId">Database identification.</param>
		/// <returns>DatabaseResponse.</returns>
		[Filters.NHibernateTransaction]
		[Authorize]
		public DatabaseResponse Get(string dbId)
		{
			Database database = null;
			User user = this.GetSewebarUser();

			if (user != null)
			{
				database = user.Databases.FirstOrDefault(d => d.Name == dbId);
			}

			return new DatabaseResponse(database);
		}

		/// <summary>
		/// Register user or database for existing user.
		/// </summary>
		/// <returns>Registered UserResponse.</returns>
		[Filters.NHibernateTransaction]
		public UserResponse Post()
		{
			var request = new UserRequest(this);
			
			User user = this.Repository.Query<User>()
				.FirstOrDefault(u => u.Username == request.UserName && u.Password == request.Password);

			if (user == null)
			{
				user = new User
					{
						Username = request.UserName,
						Password = request.Password
					};

				Repository.Add(user);
			}

			if (request.DbId != null)
			{
				user.Databases.Add(new Database
					{
						Name = request.DbId,
						Password = request.DbPassword,
						Owner = user
					});
			}

			this.Repository.Save(user);

			return new UserResponse(user);
		}

		/// <summary>
		/// Update user or change user's password.
		/// </summary>
		/// <returns>UserResponse.</returns>
		[Filters.NHibernateTransaction]
		[Authorize]
		public UserResponse Put()
		{
			// TODO: change password and username as admin
			var request = new UserRequest(this);
			var user = this.GetSewebarUser();

			if (user == null)
			{
				throw new Exception("User not found.");
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
		[Authorize]
		public Response Delete(string username)
		{
			User user = this.GetSewebarUser();

			if (user != null && user.Username == username)
			{
				// deleting himself
				this.Repository.Remove(user);

				return new Response(string.Format("User \"{0}\" removed.", user.Username));
			}
			else if (user != null && this.User.IsInRole("admin"))
			{
				// deleting by admin
			}

			return new Response(string.Format("User \"{0}\" not found.", username));
		}
    }
}
