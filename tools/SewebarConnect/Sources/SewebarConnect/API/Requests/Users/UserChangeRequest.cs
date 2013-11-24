using System;
using System.Web;
using SewebarConnect.Controllers;

namespace SewebarConnect.API.Requests.Users
{
	public class UserChangeRequest : Request
	{
		public string Link
		{
			get
			{
				return this.HttpContext.Request["email_link"];
			}
		}

		public string NewEmail
		{
			get
			{
				return GetFromRequest("new_email");
			}
		}

		public string NewUsername
		{
			get
			{
				return GetFromRequest("new_username");
			}
		}

		public string NewPassword
		{
			get
			{
				return GetFromRequest("new_password");
			}
		}

		public string EmailFrom
		{
			get
			{
				return this.HttpContext.Request["email_from"];
			}
		}

		public UserChangeRequest(ApiBaseController controller)
			: base(new HttpContextWrapper(System.Web.HttpContext.Current))
		{
		}

		private string GetFromRequest(string key)
		{
			string val = this.HttpContext.Request[key];

			if (string.IsNullOrEmpty(val))
			{
				return null;
			}

			return val;
		}

		public SewebarKey.UserPendingUpdate GetPendingUpdate(SewebarKey.User user)
		{
			return new SewebarKey.UserPendingUpdate
			{
				Link = this.Link,
				NewEmail = this.NewEmail,
				NewPassword = this.NewPassword,
				NewUsername = this.NewUsername,
				User = user,
				RequestedTime = DateTime.Now
			};
		}
	}
}