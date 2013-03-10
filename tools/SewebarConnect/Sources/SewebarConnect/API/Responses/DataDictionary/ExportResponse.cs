namespace SewebarConnect.API.Responses.DataDictionary
{
	public class ExportResponse : Response, IFileResponse
	{
		public string OutputFilePath { get; set; }

		public string GetFile()
		{
			return this.OutputFilePath;
		}
	}
}