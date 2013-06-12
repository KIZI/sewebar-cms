using System.Xml.Linq;
using SewebarKey;

namespace SewebarConnect.API.Responses.Users
{
	public class DatabaseResponse : Response
	{
		protected Database Database { get; set; }

		protected override XElement GetBody()
		{
			if (this.Database != null)
			{
				var body = new XElement("database");

				body.Add(
					new XAttribute("name", this.Database.Name),
					new XAttribute("password", this.Database.Password));

				return body;
			}
			else
			{
				return base.GetBody();
			}
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