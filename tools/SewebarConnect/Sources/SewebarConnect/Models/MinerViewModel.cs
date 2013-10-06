using LMWrapper.LISpMiner;

namespace SewebarConnect.Models
{
	public class MinerViewModel
	{
		public LISpMiner Miner { get; private set; }

		public string Id
		{
			get
			{
				return this.Miner.Id;
			}
		}

		public bool SharedPool
		{
			get
			{
				return this.Miner.SharedPool;
			}
		}

		public string Owner { get; private set; }

		internal MinerViewModel(LISpMiner miner, string owner)
		{
			this.Miner = miner;
			this.Owner = owner;
		}
	}
}