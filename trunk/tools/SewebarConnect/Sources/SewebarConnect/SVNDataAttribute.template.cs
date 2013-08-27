using System;
using System.Reflection;

namespace SewebarConnect
{
	[AttributeUsage(AttributeTargets.Assembly)]
	public class SVNDataAttribute : System.Attribute
	{
		private static SVNDataAttribute _assemblySvnData;

		public static SVNDataAttribute AssemblySVNData
		{
			get
			{
				if (_assemblySvnData == null)
				{
					object[] attributes = Assembly.GetExecutingAssembly().GetCustomAttributes(typeof(SVNDataAttribute), false);

					if (attributes.Length > 0)
					{
						_assemblySvnData = attributes[0] as SVNDataAttribute;
					}

					if (_assemblySvnData == null)
					{
						throw new Exception("Assembly should have exactly one SVN Data attribute");
					}
				}

				return _assemblySvnData;
			}
		}

		/*
		WCREV = $WCREV$
		WCDATE = $WCDATE$
		WCNOW = $WCNOW$
		WCRANGE = $WCRANGE$
		WCMIXED = $WCMIXED?Mixed update revision:Not mixed$
		WCMODS = $WCMODS?Modified:Not modified$
		WCURL = $WCURL$
		WCINSVN = $WCINSVN?Versioned:Not Versioned$
		WCNEEDSLOCK = $WCNEEDSLOCK?Lock Required:Lock not required$
		WCISLOCKED = $WCISLOCKED?Locked:Not Locked$
		WCLOCKDATE = $WCLOCKDATE$
		WCLOCKOWNER = $WCLOCKOWNER$
		WCLOCKCOMMENT = $WCLOCKCOMMENT$
		*/

		public string WCREV
		{
			get { return "$WCREV$"; }
		}

		public string WCDATE
		{
			get { return "$WCDATE$"; }
		}

		public string WCNOW
		{
			get { return "$WCNOW$"; }
		}
		public string WCRANGE
		{
			get { return "$WCRANGE$"; }
		}

		public string WCMODS
		{
			get { return "$WCMODS$"; }
		}

		public string WCURL
		{
			get { return "$WCURL$"; }
		}

		public string WCLOCKCOMMENT
		{
			get { return "$WCLOCKCOMMENT$"; }
		}

		public SVNDataAttribute()
		{
		}
	}
}
