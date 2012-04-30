using LMWrapper.ODBC;

namespace LMWrapper
{
	interface IMetabase
	{
		void SetDatabaseDsnToMetabase(OdbcConnection database);
	}
}
