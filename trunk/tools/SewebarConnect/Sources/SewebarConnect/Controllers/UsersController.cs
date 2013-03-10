using System.Linq;
using System.Web.Mvc;
using System.Xml.Linq;
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
        public ActionResult Users()
	    {
		    var users = Repository.FindAll<SewebarKey.User>();

		    var doc = new XDocument(
			    new XDeclaration("1.0", "utf-8", "yes"),
			    new XElement("users",
			                 users.Select(FromUser)
				    )
			    );

			return this.Content(doc.ToString());
	    }

		[Filters.NHibernateTransaction]
		public ActionResult Add()
		{
			var user = new SewebarKey.User
				           {
					           Username = "Andrej",
					           Password = "*****"
				           };

			Repository.Add(user);

			return RedirectToAction("Users");
		}

		private static XElement FromUser(SewebarKey.User user)
		{
			return new XElement("user",
			                    new XElement("name", user.Username),
								new XElement("password", user.Password)
				);
		}
    }
}
