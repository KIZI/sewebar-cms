using System;
using System.IO;
using System.Net;
using System.Text;
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
		                              		"LISp-Miner.Core.zip"
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

		public string Update()
		{
			var sb = new StringBuilder();
			var directory = String.Format("{0}\\LISp Miner {1}", this.TargetPath, this.ReleaseDate.ToString("yyyy.MM.dd"));
			var current = String.Format("{0}\\LISp Miner", this.TargetPath);

			var outputBuffer = string.Format("Updating LISp Miner to version {0} from {1}", this.Version, this.ReleaseDate.ToShortDateString());
			sb.AppendLine(outputBuffer);
			Console.WriteLine(outputBuffer);

			outputBuffer = string.Format("\tto destination: {0}", Path.GetFullPath(this.TargetPath));
			sb.AppendLine(outputBuffer);
			Console.WriteLine(outputBuffer);

			if (Directory.Exists(directory))
			{
				Directory.Delete(directory, true);
			}

			Directory.CreateDirectory(directory);

			foreach (var package in Packages)
			{
				this.DownloadPackage(directory, package, sb);
			}

			if (Directory.Exists(current))
			{
				Directory.Delete(current, true);
			}

			outputBuffer = string.Format("Setting LISp Miner version {0} as current.", this.Version);
			sb.AppendLine(outputBuffer);
			Console.WriteLine(outputBuffer);

			DirectoryUtil.Copy(directory, current);

			return sb.ToString();
		}

		private void DownloadPackage(string directory, string package, StringBuilder output)
		{
			var outputBuffer = String.Format("Downloading {0} ...", package);
			output.AppendLine(outputBuffer);
			Console.WriteLine(outputBuffer);

			var source = String.Format("{0}/{1}", FilesPath, package);
			var destination = String.Format("{0}\\{1}", directory, package);

			this.Client.DownloadFile(source, destination);

			outputBuffer = String.Format("Unpacking {0} ...", package);
			output.AppendLine(outputBuffer);
			Console.WriteLine(outputBuffer);
			ZipUtil.Unzip(directory, destination);

			File.Delete(destination);
		}
	}
}
