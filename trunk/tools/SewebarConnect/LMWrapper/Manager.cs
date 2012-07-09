using System;
using System.IO;
using System.Net;
using System.Text.RegularExpressions;
using LMWrapper.Utils;

namespace LMWrapper
{
	public class Manager
	{
		private const string VersionPath = "http://lispminer.vse.cz/download/index.php";
		private const string FilesPath = "http://lispminer.vse.cz/files/exe/";

		protected string[] Packages = new string[]
		                              	{
		                              		"LM.4ft.zip",
											"LM.TaskPooler.zip",
											"LM.Sewebar.zip",
											"LMEmpty.zip"
		                              	};

		public string TargetPath { get; private set; }

		public string Version { get; private set; }

		public DateTime ReleaseDate { get; private set; }

		private WebClient Client { get; set; }

		public Manager(string targetPath)
		{
			this.TargetPath = targetPath;
			this.Client = new WebClient();

			var page = this.Client.DownloadString(VersionPath);

			Match match = Regex.Match(page, "<p>The LISp-Miner System, version <a href=\"relnotes.php\"><b>(.*)</b></a> from (.*) available.</p>");

			if (match.Success)
			{
				this.Version = match.Groups[1].ToString();
				this.ReleaseDate = DateTime.Parse(match.Groups[2].ToString());
			}
		}

		public void Update()
		{
			var directory = String.Format("{0}\\LISp Miner {1}", this.TargetPath, this.ReleaseDate.ToString("yyyy.MM.dd"));
			var current = String.Format("{0}\\LISp Miner", this.TargetPath);

			Console.WriteLine(String.Format("Updating LISp Miner to version {0} from {1}", this.Version, this.ReleaseDate.ToShortDateString()));
			Console.WriteLine(String.Format("\tto destination: {0}", Path.GetFullPath(this.TargetPath)));

			if (Directory.Exists(directory))
			{
				Directory.Delete(directory, true);
			}

			Directory.CreateDirectory(directory);

			foreach (var package in Packages)
			{
				this.DownloadPackage(directory, package);
			}

			if (Directory.Exists(current))
			{
				Directory.Delete(current, true);
			}

			Console.WriteLine(String.Format("Setting LISp Miner version {0} as current.", this.Version));

			DirectoryUtil.Copy(directory, current);
		}

		private void DownloadPackage(string directory, string package)
		{
			Console.WriteLine(String.Format("Downloading {0} ...", package));

			var source = String.Format("{0}/{1}", FilesPath, package);
			var destination = String.Format("{0}\\{1}", directory, package);

			this.Client.DownloadFile(source, destination);

			Console.WriteLine(String.Format("Unpacking {0} ...", package));
			ZipUtil.Unzip(directory, destination);

			File.Delete(destination);
		}
	}
}
