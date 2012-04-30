using System;
using System.IO;
using LMWrapper.LISpMiner;

namespace Debugger
{
	class Program
	{
		static void Main(string[] args)
		{
			var env = new LMWrapper.Environment
			{
				//LMPoolPath = String.Format(@"{0}", @"C:\LMs\"),
				LMPoolPath = String.Format(@"{0}", @"d:\svn\sewebar-cms\tools\SewebarConnect\Data\LMs"),
				LMPath = String.Format("{0}/../{1}", System.AppDomain.CurrentDomain.BaseDirectory, "LISp Miner"),
			};

			foreach (var path in Directory.GetDirectories(env.LMPoolPath))
			{
				try
				{
					var directory = new DirectoryInfo(path);
					var lm = new LISpMiner(directory, env);

					lm.Dispose();

					Console.WriteLine(lm.Id);
				}
				catch (Exception ex)
				{
					Console.WriteLine(String.Format("skipping {0} {1}", path, ex.Message));
				}
			}

			Console.WriteLine("Done.");

			Console.Read();
		}
	}
}
