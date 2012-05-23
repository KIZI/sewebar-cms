namespace LMWrapper.LISpMiner
{
	public interface ITaskLauncher
	{
		ExecutableStatus Status { get; }

		string TaskName { get; set; }

		int KeepAlive { get; set; }

		void Execute();
	}
}
