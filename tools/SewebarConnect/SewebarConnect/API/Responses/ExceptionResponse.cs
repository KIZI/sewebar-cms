namespace SewebarConnect.API.Responses
{
	public class ExceptionResponse : Response
	{
		public ExceptionResponse(string message)
		{
			this.Message = message;
			this.Status = Status.Failure;
		}
	}
}