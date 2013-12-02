using System;
using System.Net.Mail;
using System.Xml.Linq;

namespace SewebarConnect.API.Responses.Users
{
	public class UserUpdateResponse : Response
	{
		private static readonly SmtpClient Client = new SmtpClient();

		private SewebarKey.UserPendingUpdate Update { get; set; }

		private string From { get; set; }

		public UserUpdateResponse(SewebarKey.UserPendingUpdate update, string from)
		{
			this.Update = update;
			this.From = from;

			this.Message = string.Format("User has been notified via mail {0}", Update.User.Email);
		}

		protected override XElement GetBody()
		{
			try
			{
				if (string.IsNullOrEmpty(Update.User.Email))
				{
					throw new Exception(string.Format("User '{0}' has no email to send forgotten password.", Update.User.Username));
				}
			
				SendNotification();
				
				return base.GetBody();	
			}
			catch (Exception ex)
			{
				this.Status = Status.Failure;
				this.Message = string.Format("User could not been notified because of error: {0}", ex.Message);

				return base.GetBody();
			}
		}

		private void SendNotification()
		{
			var link = Update.Link.Replace("{code}", Update.Id.ToString());

			var mail = new MailMessage(From, Update.User.Email)
			{
				Subject = "EasyMiner forgotten password",
				Body = string.Format("Click here {0}", link)
			};

			Client.Send(mail);
		}
	}
}