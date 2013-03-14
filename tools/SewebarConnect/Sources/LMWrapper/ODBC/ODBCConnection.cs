using System.IO;

namespace LMWrapper.ODBC
{
	public abstract class OdbcConnection
	{
		protected string DSNFile { get; private set; }

		protected string DSNFileWithoutExtension { get; private set; }

		internal virtual ConnectionString ConnectionString { get; private set; }

		protected OdbcConnection(string dsnFile)
		{
			this.DSNFile = Path.GetFullPath(dsnFile);
			this.DSNFileWithoutExtension = Path.Combine(Directory.GetParent(this.DSNFile).FullName, Path.GetFileNameWithoutExtension(this.DSNFile));
			this.ConnectionString = new ConnectionString(string.Format("FILEDSN={0}", this.DSNFileWithoutExtension));
		}

		public virtual void Init()
		{
		}

		public virtual void Destroy()
		{
			if (File.Exists(this.DSNFile))
			{
				File.Delete(this.DSNFile);
			}
		}
	}
}
