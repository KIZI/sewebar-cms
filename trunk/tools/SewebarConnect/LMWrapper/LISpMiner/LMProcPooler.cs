namespace LMWrapper.LISpMiner
{
	/// <summary>
	/// TODO: implement
	/// </summary>
	public class LMProcPooler : Executable, ITaskLauncher
    {
		public override string Arguments
		{
			get { throw new System.NotImplementedException(); }
		}

		public string TaskName { get; set; }

		public bool TaskCancel { get; set; }
		
		public bool CancelAll { get; set; }

		public int KeepAlive { get; set; }
    }
}
