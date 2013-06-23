namespace SewebarConnect.API.Requests.Task
{
    internal class TaskCancelationRequest : Request
    {
        public TaskUpdateRequest Request { get; private set; }

        public TaskCancelationRequest(TaskUpdateRequest request)
            : base(request.Controller.LISpMiner, request.HttpContext)
        {
            this.Request = request;
        }
    }
}