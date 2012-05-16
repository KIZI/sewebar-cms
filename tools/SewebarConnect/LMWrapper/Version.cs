namespace LMWrapper
{
	public class Version
	{
		private string _version;

		public Version(string template)
		{
			this._version = template;
		}

		public override string ToString()
		{
			return this._version;
		}
	}
}
