using System;
using System.Collections.Generic;
using System.IO;
using System.Text;

namespace LMWrapper.ODBC
{
	public class AccessConnection : ODBCConnection
	{
		private static int i = 0;

		public override string DSN
		{
			get
			{
				var dsn = base.DSN;

				if (!ODBCManagerRegistry.DSNExists(dsn) && !File.Exists(this.Path))
				{
					File.Copy(FromFile, this.Path, true);

					ODBCManagerRegistry.CreateDSN(dsn, "", "Microsoft Access Driver (*.mdb)", this.Path);
				}

				return dsn;
			}
			set
			{
				base.DSN = value;
			}
		}

		public string Path { get; protected set; }

		protected string FromFile { get; set; }

		public override string ConnestionString
		{
			get
			{
				var s = this.DSN;
				return String.Format("DSN=c{0}", i++);
			}
		}

		public AccessConnection(string file)
		{
			this.Path = file;
		}

		public AccessConnection(string file, string fromFile) : this(file)
		{
			this.FromFile = fromFile;
		}

		public override void Dispose()
		{
			File.Delete(this.Path);

			base.Dispose();
		}
	}
}
