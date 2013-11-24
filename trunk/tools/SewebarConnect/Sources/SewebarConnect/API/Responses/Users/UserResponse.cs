using System.Linq;
using System.Xml.Linq;

namespace SewebarConnect.API.Responses.Users
{
	public class UserResponse : Response
	{
		private SewebarKey.User User { get; set; }

		public UserResponse(SewebarKey.User user)
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
								new XElement("email", user.Email),
								new XElement("databases", user.Databases.Select(FromDatabase)),
								new XElement("miners", user.Miners.Select(FromMiner))
				);
		}

		private static object FromMiner(SewebarKey.Miner miner)
		{
			return new XElement("miner",
				new XAttribute("id", miner.MinerId));
		}

		private static object FromDatabase(SewebarKey.Database db)
		{
			return new XElement("database",
								new XAttribute("id", db.Name));
		}
	}
}