using System.Linq;
using System.Web.Mvc;
using System.Web.Security;
using SewebarConnect.Models;
using SewebarKey.Repositories;

namespace SewebarConnect.Controllers
{
	[AllowAnonymous]
	public class LoginController : Controller
	{
		private IRepository _repository;

		protected virtual IRepository Repository
		{
			get { return _repository ?? (_repository = new NHibernateRepository(MvcApplication.SessionFactory.GetCurrentSession())); }
		}

		[HttpGet]
		public ActionResult Index(string returnUrl = null)
		{
			this.ViewBag.ReturnUrl = returnUrl;
			return View();
		}

		[HttpPost]
		[Filters.Mvc.NHibernateTransaction]
		public ActionResult Index(LogInViewModel model, string returnUrl)
		{
			if (this.ModelState.IsValid)
			{
				SewebarKey.User user = this.Repository.Query<SewebarKey.User>()
					.FirstOrDefault(u => u.Username == model.UserName && u.Password == model.Password);

				if (user != null && user.IsAdmin)
				{
					FormsAuthentication.SetAuthCookie(model.UserName, false);

					return this.Redirect(returnUrl);	
				}
			}

			this.ViewBag.ReturnUrl = returnUrl;
			this.ModelState.AddModelError("", "The user name or password provided is incorrect.");

			return this.View(model);
		}

		public ActionResult Logout()
		{
			FormsAuthentication.SignOut();
			return this.Redirect("~/");
		}
	}
}
