using System.Runtime.Serialization;

namespace LMWrapper.ODBC
{
	[DataContract]
	public class DbConnection
	{
		[DataMember(Name = "type")]
		public OdbcDrivers Type { get; set; }

		[DataMember(Name = "server")]
		public string Server { get; set; }

		[DataMember(Name = "database")]
		public string Database { get; set; }

		[DataMember(Name = "username")]
		public string Username { get; set; }

		[DataMember(Name = "password")]
		public string Password { get; set; }
	}
}
