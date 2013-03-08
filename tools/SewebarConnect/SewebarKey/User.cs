using System;
using System.Collections.Generic;

namespace SewebarKey
{
	public class User
	{
		public virtual Guid Id { get; set; }

		public virtual string Username { get; set; }

		public virtual string Password { get; set; }

		public virtual ISet<Database> Databases { get; set; }

		public virtual ISet<Miner> Miners { get; set; }
	}
}
