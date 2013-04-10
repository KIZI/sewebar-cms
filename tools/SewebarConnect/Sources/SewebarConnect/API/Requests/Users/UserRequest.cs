using System.Web;

namespace SewebarConnect.API.Requests.Users
{
	/// <summary>
	/// POST name=username1&password=pwd1&db_id=database1&db_password=uknown
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

		public UserRequest(HttpContextBase httpContext) : base(httpContext)
		{
		}
	}
}