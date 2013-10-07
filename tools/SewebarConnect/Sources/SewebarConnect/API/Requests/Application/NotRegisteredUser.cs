using System;
using System.Text;
using System.Web;

namespace SewebarConnect.API.Requests.Application
{
	public class NotRegisteredUser
	{
		internal static NotRegisteredUser FromRequest(HttpRequestBase request)
		{
			string basicAuthToken = request.Headers["Authorization"];

			if (!string.IsNullOrEmpty(basicAuthToken))
			{
				basicAuthToken = basicAuthToken.Replace("Basic ", string.Empty);

				Encoding encoding = Encoding.GetEncoding("iso-8859-1");
				string userPass = encoding.GetString(Convert.FromBase64String(basicAuthToken));
				int separator = userPass.IndexOf(':');

				return new NotRegisteredUser(userPass.Substring(0, separator), userPass.Substring(separator + 1));
			}

			return null;
		}

		public string Username { get; private set; }

		public string Password { get; private set; }

		internal NotRegisteredUser(string username, string password)
		{
			this.Username = username;
			this.Password = password;
		}
	}
}