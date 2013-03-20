using System.Collections.Generic;
using System.Linq;
using System.Xml.Linq;
using SewebarKey;

namespace SewebarConnect.API.Responses.Users
{
	public class UsersResponse : Response
	{
		private IEnumerable<User> Users { get; set; }

		protected override XElement GetBody()
		{
			return new XElement("users",
			                    Users.Select(UserResponse.FromUser)
				);
		}

		public UsersResponse(IEnumerable<User> users)
		{
			Users = users;
		}
	}
}