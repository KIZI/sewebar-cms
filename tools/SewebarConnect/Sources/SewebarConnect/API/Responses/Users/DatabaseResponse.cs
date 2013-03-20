using System.Xml.Linq;
using SewebarKey;

namespace SewebarConnect.API.Responses.Users
{
	public class DatabaseResponse : Response
	{
		protected Database Database { get; set; }

		protected override XElement GetBody()
		{
			var body = new XElement("database");

			if (this.Database != null)
			{
				body.Add(
					new XAttribute("name", this.Database.Name),
					new XAttribute("password", this.Database.Password));
			}

			return body;
		}

		public DatabaseResponse(Database db)
		{
			this.Database = db;

			this.Status = this.Database == null ? Status.Failure : Status.Success;

			if (this.Status == Status.Failure)
			{
				this.Message = "Wrong username, password or database ID.";
			}
		}
	}
}