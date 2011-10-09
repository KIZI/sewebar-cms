using System;
using System.Collections.Generic;
using LMWrapper.ODBC;

namespace LMWrapper
{
	public class Environment
	{
		/// <summary>
		/// TODO: also try to load miners from existing folders (registered before Global.Application_Start() and maybe session).
		/// </summary>
		private Dictionary<string, LISpMiner.LISpMiner> _registeredMiners;

		public string LMPoolPath { get; set; }

		public string DataPath { get; set; }

		public string LMPath { get; set; }

		public bool IsMono { get; set; }

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
		/// TODO: make protected
		/// </summary>
		/// <param name="guid"></param>
		/// <param name="database"></param>
		/// <returns></returns>
		public string Register(LISpMiner.LISpMiner miner)
		{
			RegisteredMiners.Add(miner.Id, miner);

			return miner.Id;
		}

		public LISpMiner.LISpMiner GetMiner(string guid)
		{
			return this.RegisteredMiners[guid];
		}
	}
}
