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

		[Filters.NHibernateTransaction]
		public UsersResponse Get()
		{
			var users = Repository.FindAll<SewebarKey.User>();

			return new UsersResponse(users);
		}

		[Filters.NHibernateTransaction]
		public DatabaseResponse Get([FromUri]string db_id, [FromUri]string name = "", [FromUri]string password = "")
		{
			Database database = null;

			User user = this.Repository.Query<User>()
				.FirstOrDefault(u => u.Username == (name ?? "") && u.Password == (password ?? ""));

			if (user != null)
			{
				database = user.Databases.FirstOrDefault(d => d.Name == db_id);
			}

			return new DatabaseResponse(database);
		}

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

			Repository.Add(user);

			return new UserResponse(user);
		}

		[Filters.NHibernateTransaction]
		public UserResponse Put()
		{
			var request = new UserRequest(this);

			User user = this.Repository.Query<User>()
				.FirstOrDefault(u => u.Username == request.UserName && u.Password == request.Password);

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
    }
}
