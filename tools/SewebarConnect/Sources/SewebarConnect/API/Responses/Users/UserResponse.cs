using System.Linq;
using System.Xml.Linq;
using SewebarKey;

namespace SewebarConnect.API.Responses.Users
{
	public class UserResponse : Response
	{
		private User User { get; set; }

		public UserResponse(User user)
		{
			User = user;
		}

		protected override XElement GetBody()
		{
			return FromUser(User);
		}

		internal static XElement FromUser(SewebarKey.User user)
		{
			return new XElement("user",
								new XElement("username", user.Username),
								new XElement("databases", user.Databases.Select(FromDatabase))
				);
		}

		private static object FromDatabase(Database db)
		{
			return new XElement("database",
			                    new XAttribute("id", db.Name));
		}
	}
}