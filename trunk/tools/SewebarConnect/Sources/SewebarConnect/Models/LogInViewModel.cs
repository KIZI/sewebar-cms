using System.ComponentModel.DataAnnotations;

namespace SewebarConnect.Models
{
	public class LogInViewModel
	{
		[Display(Name = "User Name")]
		[Required]
		public string UserName { get; set; }

		[Display(Name = "Password")]
		[DataType(DataType.Password)]
		public string Password { get; set; }
	}
}