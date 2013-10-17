using System.IO;

namespace LMWrapper
{
	public class PCGridSettings
	{
		public string Binaries { get; private set; }

		public string KeyStore { get; private set; }

		public string Alias { get; private set; }

		public string Password { get; private set; }

		public string Hostname { get; private set; }

		public string Port { get; private set; }

		public PCGridSettings(string binaries, string keystore, string hostname, string port, string alias, string password)
		{
			this.Binaries = binaries;
			this.KeyStore = keystore;
			this.Hostname = hostname;
			this.Port = port;
			this.Alias = alias;
			this.Password = password;
		}

		public string GetSettingsPath(string main)
		{
			return string.Format(@"{0}\SewebarConnect.grid.settings", main);
		}
	}
}