using System.Collections.Generic;
using System.IO;

namespace LMWrapper
{
	public class Environment
	{
		private Dictionary<string, LISpMiner.LISpMiner> _registeredMiners;
		private string _lmPath;

		public string LMPoolPath { get; set; }

		public string DataPath { get; set; }

		public string LMPath
		{
			get
			{
				return _lmPath;
			}

			set
			{
				_lmPath = new DirectoryInfo(value).FullName;
			}
		}

		public string PCGridPath { get; set; }

		public bool TimeLog { get; set; }

		public Dictionary<string, LISpMiner.LISpMiner>.KeyCollection ExistingMiners
		{
			get { return this.RegisteredMiners.Keys; }
		}

		public Dictionary<string, LISpMiner.LISpMiner>.ValueCollection Miners
		{
			get { return this.RegisteredMiners.Values; }
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
