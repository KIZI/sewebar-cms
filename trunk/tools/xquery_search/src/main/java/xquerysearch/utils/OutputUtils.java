package xquerysearch.utils;


/**
 * Help class for outputting.
 * 
 * @author Tomas Marek
 * 
 */
public class OutputUtils {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private OutputUtils() {
		// TODO Auto-generated constructor stub
	}

	/**
	 * Helps convert 2-dimension array of doubles to string.
	 * 
	 * @param queryCompliance
	 * @return
	 */
	public static String getQueryComplianceForOutput(double[][] queryCompliance) {
		StringBuffer ret = new StringBuffer();
		ret.append("[");
		if (queryCompliance != null) {
			for (int i = 0; i < queryCompliance.length; i++) {
				if (i > 0) {
					ret.append(", ");
				}
				ret.append("[");
				for (int j = 0; j < queryCompliance[i].length; j++) {
					if (j > 0) {
						ret.append(", ");
					}
					ret.append(queryCompliance[i][j]);
				}
				ret.append("]");
			}
		}
		ret.append("]");
		return ret.toString();
	}
}
