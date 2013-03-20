using System.Collections.Generic;
using System.Linq;
using System.Web.Mvc;
using SewebarConnect.API;
using SewebarConnect.API.Requests.Users;
using SewebarConnect.API.Responses.Users;
using SewebarKey;
using SewebarKey.Repositories;

namespace SewebarConnect.Controllers
{
    public class UsersController : BaseController
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
		public ActionResult Register()
		{
			var request = new UserRequest(this.HttpContext);
			
			// TODO add query
			IEnumerable<User> users = this.Repository.FindAll<User>();
			User user = users.FirstOrDefault(u => u.Username == request.UserName && u.Password == request.Password);

			if (user == null)
			{
				user = new User
					{
						Username = request.UserName,
						Password = request.Password
					};
			}

			user.Databases.Add(new Database
			{
				Name = request.DbId,
				Password = request.DbPassword,
				Owner = user
			});

			Repository.Add(user);

			return List();
		}

		[Filters.NHibernateTransaction]
		public ActionResult Get(string name, string password, string db_id)
		{
			Database database = null;

			// TODO add query
			IEnumerable<User> users = this.Repository.FindAll<User>();
			User user = users.FirstOrDefault(u => u.Username == name && u.Password == password);
			
			if (user != null)
			{
				database = user.Databases.FirstOrDefault(d => d.Name == db_id);
			}

			return new XmlResult
				{
					Data = new DatabaseResponse(database)
				};
		}

	    [Filters.NHibernateTransaction]
        public ActionResult List()
	    {
		    var users = Repository.FindAll<SewebarKey.User>();

			return new XmlResult
				{
					Data = new UsersResponse(users)
				};
	    }
    }
}
