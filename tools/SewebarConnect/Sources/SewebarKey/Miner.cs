using System;

namespace SewebarKey
{
	public class Miner
	{
		public virtual Guid Id { get; set; }

		public virtual string Path { get; set; }

		public virtual User Owner { get; set; }
	}
}
