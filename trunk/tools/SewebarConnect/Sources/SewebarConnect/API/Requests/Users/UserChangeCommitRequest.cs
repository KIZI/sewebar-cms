using System;
using System.Web;

namespace SewebarConnect.API.Requests.Users
{
	public class UserChangeCommitRequest : Request
	{
		public Guid Id
		{
			get
			{
				Guid id;
				var code = this.HttpContext.Request["code"];

				if (!string.IsNullOrEmpty(code) && Guid.TryParse(code, out id))
				{
					return id;
				}

				return Guid.Empty;
			}
		}

		public UserChangeCommitRequest() 
			: base(new HttpContextWrapper(System.Web.HttpContext.Current))
		{
		}
	}
}