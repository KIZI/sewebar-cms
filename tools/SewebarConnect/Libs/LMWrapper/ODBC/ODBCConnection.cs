using System;
using System.Collections.Generic;
using System.Text;

namespace LMWrapper.ODBC
{
	public abstract class ODBCConnection : IDisposable
	{
		public static ODBCConnection Create()
		{
			return null;
		}

		public virtual string DSN { get; set; }

		public abstract string ConnestionString { get; }

		public virtual void Dispose()
		{
			
		}
	}
}
