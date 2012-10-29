package xquerysearch.transformation;

import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.List;

import xquerysearch.domain.Cluster;
import xquerysearch.domain.grouping.Group;
import xquerysearch.domain.result.Result;

/**
 * Transformer used to transform result data to response-friendly form.
 * 
 * @author Tomas Marek
 * 
 */
public class OutputTransformer {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private OutputTransformer() {
	}

	/**
	 * Transforms list of items (being instance of {@link Result},
	 * {@link Groups} or {@link Cluster}) to response-friendly form represented
	 * as String.
	 * 
	 * @param groups
	 * @param queryTime
	 * @param docCount
	 * @param arCount
	 * @return
	 */
	public static String transformObjectsList(List<? extends Object> list, long queryTime, long docCount, long arCount) {
		StringBuffer ret = new StringBuffer();
		appendHeaderOfSearch(ret, queryTime, docCount, arCount);
		ret.append("<Hits>");
		if (list != null) {
			for (Object item : list) {
				if (item != null && item instanceof Result) {
					ret.append(((Result) item).toString());
				} else if (item != null && item instanceof Group) {
					ret.append(((Group) item).toString());
				} else if (item != null && item instanceof Cluster) {
					ret.append(((Cluster) item).toString());
				}
			}
		}
		ret.append("</Hits></SearchResult>");
		return ret.toString();
	}

	/**
	 * Creates header for search result output. TODO better output handling (for
	 * whole SearchResult element)
	 * 
	 * @param sb
	 * @param queryTime
	 * @param docCount
	 * @param arCount
	 */
	private static void appendHeaderOfSearch(StringBuffer sb, long queryTime, long docCount, long arCount) {
		sb.append("<SearchResult xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:noNamespaceSchemaLocation=\"http://sewebar.vse.cz/schemas/SearchResult0_2.xsd\">");
		sb.append("<Metadata>");
		sb.append("<SearchTimestamp>" + getActualTimestamp() + "</SearchTimestamp>");
		sb.append("<LastIndexUpdate>2011-05-30T09:00:00</LastIndexUpdate>");
		sb.append("<SearchAlgorithm>xquery</SearchAlgorithm>");
		sb.append("<SearchAlgorithmVersion>xquery 3/4/2011</SearchAlgorithmVersion>");
		sb.append("</Metadata><Statistics>");
		sb.append("<ExecutionTime>" + queryTime + "</ExecutionTime>");
		sb.append("<DocumentsSearched>" + docCount + "</DocumentsSearched>");
		sb.append("<RulesSearched>" + arCount + "</RulesSearched>");
		sb.append("</Statistics>");
	}

	/**
	 * @return actual timestamp in <tt>yyyy-MM-dd'T'HH:mm:ss</tt> format
	 */
	private static String getActualTimestamp() {
		SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss");
		return sdf.format(new Date());
	}

}
