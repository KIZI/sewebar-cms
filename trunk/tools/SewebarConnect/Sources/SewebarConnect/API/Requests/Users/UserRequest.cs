using System.Web;
using SewebarConnect.API.Requests.Application;
using SewebarConnect.Controllers;

namespace SewebarConnect.API.Requests.Users
{
	/// <summary>
	/// POST name=username1&password=pwd1&db_id=database1&db_password=uknown
	/// TODO: UserRequest as XML
	/// </summary>
	public class UserRequest : Request
	{
		public override string UserName
		{
			get { return this.HttpContext.Request["name"]; }
		}

		public override string Password
		{
			get { return this.HttpContext.Request["password"]; }
		}

		protected string Email
		{
			get { return this.HttpContext.Request["email"]; }
		}

		public string NewUserName
		{
			get { return this.HttpContext.Request["new_name"]; }
		}

		public string NewPassword
		{
			get { return this.HttpContext.Request["new_password"]; }
		}

		public string DbId
		{
			get { return this.HttpContext.Request["db_id"]; }
		}

		public string DbPassword
		{
			get { return this.HttpContext.Request["db_password"]; }
		}

		public NotRegisteredUser Owner
		{
			get
			{
				return NotRegisteredUser.FromRequest(this.HttpContext.Request);
			}
		}

		public UserRequest(ApiBaseController controller)
			: base(new HttpContextWrapper(System.Web.HttpContext.Current))
		{
		}

		public SewebarKey.Database GetDatabase(SewebarKey.User owner)
		{
			if (this.DbId == null)
			{
				return null;
			}

			return new SewebarKey.Database
				{
					Name = this.DbId,
					Password = this.DbPassword,
					Owner = owner
				};
		}

		public SewebarKey.User GetUser()
		{
			return new SewebarKey.User
				{
					Username = this.UserName,
					Password = this.Password,
					Email = this.Email
				};
		}
	}
}