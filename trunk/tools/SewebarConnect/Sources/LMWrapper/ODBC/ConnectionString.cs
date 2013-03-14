namespace LMWrapper.ODBC
{
	internal class ConnectionString
	{
		public string Value { get; private set; }

		public ConnectionString(string value)
		{
			this.Value = value;
		}
	}
}
