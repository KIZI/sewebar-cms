namespace SewebarConnect.API.Responses.Task
{
	public class TaskResponse : Response, IFileResponse
	{
		public string OutputFilePath { get; set; }

		public string GetFile()
		{
			return this.OutputFilePath;
		}
	}
}