using System.Xml.Linq;
using LMWrapper.ODBC;
using NUnit.Framework;

namespace SewebarConnectTests.API.Requests.Application
{
	[TestFixture]
	class RegistrationRequestTests
	{
		[Test]
		public void TestGetDbConnection()
		{
			// TODO: mock ApiController and create RegistrationRequest instance
			string xml = @"<?xml version=""1.0"" encoding=""utf-8""?>
						<RegistrationRequest>
							<Metabase type=""Access"">
								<File>LM Barbora.mdb</File>
							</Metabase>
							<Connection type=""MySQL"">
								<Server></Server>
								<Database></Database>
								<Username></Username>
								<Password></Password>
							</Connection>
						</RegistrationRequest>";

			XDocument doc = XDocument.Parse(xml);

			var dbConnection = SewebarConnect.API.Requests.Application.RegistrationRequest.GetDbConnection("Connection", doc);
			var dbMetabase = SewebarConnect.API.Requests.Application.RegistrationRequest.GetDbConnection("Metabase", doc);

			Assert.NotNull(dbConnection);
			Assert.AreEqual(dbConnection.Type, OdbcDrivers.MySqlConnection);

			Assert.NotNull(dbMetabase);
			Assert.AreEqual(dbMetabase.Type, OdbcDrivers.AccessConnection);
		}
	}
}
