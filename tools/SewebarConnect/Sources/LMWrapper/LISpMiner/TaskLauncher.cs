namespace LMWrapper.LISpMiner
{
	public interface ITaskLauncher
	{
		ExecutableStatus Status { get; }

		string TaskName { get; set; }

		bool TaskCancel { get; set; }

		bool CancelAll { get; set; }

		int KeepAlive { get; set; }

		void Execute();
	}
}
