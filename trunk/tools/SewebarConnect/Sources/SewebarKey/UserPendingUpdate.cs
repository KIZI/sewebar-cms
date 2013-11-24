using System;

namespace SewebarKey
{
	public class UserPendingUpdate
	{
		public virtual Guid Id { get; set; }

		public virtual User User { get; set; }

		public virtual string NewUsername { get; set; }

		public virtual string NewPassword { get; set; }

		public virtual string NewEmail { get; set; }

		public virtual string Link { get; set; }

		public virtual DateTime RequestedTime { get; set; }
	}
}
