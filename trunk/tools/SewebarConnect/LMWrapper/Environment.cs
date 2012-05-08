using System.Collections.Generic;

namespace LMWrapper
{
	public class Environment
	{
		private Dictionary<string, LISpMiner.LISpMiner> _registeredMiners;

		public string LMPoolPath { get; set; }

		public string DataPath { get; set; }

		public string LMPath { get; set; }

		public Dictionary<string, LISpMiner.LISpMiner>.KeyCollection ExistingMiners
		{
			get { return this.RegisteredMiners.Keys; }
		}

		protected Dictionary<string, LISpMiner.LISpMiner> RegisteredMiners
		{
			get
			{
				if (_registeredMiners == null)
				{
					_registeredMiners = new Dictionary<string, LISpMiner.LISpMiner>();
				}

				return _registeredMiners;
			}
		}

		/// <summary>
		/// Registers existing miner.
		/// </summary>
		/// <param name="miner">LISpMiner to register.</param>
		/// <returns>ID of registered LISpMiner.</returns>
		public string Register(LISpMiner.LISpMiner miner)
		{
			RegisteredMiners.Add(miner.Id, miner);

			return miner.Id;
		}

		/// <summary>
		/// Registers existing miner.
		/// </summary>
		/// <param name="miner">LISpMiner to register.</param>
		/// <returns>ID of registered LISpMiner.</returns>
		public void Unregister(LISpMiner.LISpMiner miner)
		{
			var m = this.GetMiner(miner.Id);

			if (m != null)
			{
				RegisteredMiners.Remove(miner.Id);
				miner.Dispose();
			}
		}

		public bool Exists(string guid)
		{
			return this.RegisteredMiners.ContainsKey(guid);
		}

		public LISpMiner.LISpMiner GetMiner(string guid)
		{
			return this.RegisteredMiners[guid];
		}
	}
}
