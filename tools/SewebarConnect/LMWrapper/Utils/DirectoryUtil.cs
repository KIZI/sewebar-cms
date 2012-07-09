using System.IO;

namespace LMWrapper.Utils
{
	public static class DirectoryUtil
	{
		public static void Copy(string sourceFolder, string destFolder)
		{
			if (!Directory.Exists(destFolder))
			{
				Directory.CreateDirectory(destFolder);
			}

			foreach (string folder in Directory.GetDirectories(sourceFolder))
			{
				string name = Path.GetFileName(folder);

				if (name == null) continue;

				string dest = Path.Combine(destFolder, name);
				Copy(folder, dest);
			}

			foreach (string file in Directory.GetFiles(sourceFolder))
			{
				string name = Path.GetFileName(file);

				if (name == null) continue;

				string dest = Path.Combine(destFolder, name);
				File.Copy(file, dest, true);
			}
		}
	}
}
