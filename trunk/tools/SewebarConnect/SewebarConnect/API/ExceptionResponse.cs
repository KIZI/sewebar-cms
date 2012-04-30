using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace SewebarConnect.API
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