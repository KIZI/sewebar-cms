package xquerysearch.transformation;

import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.List;

import xquerysearch.domain.Cluster;
import xquerysearch.domain.grouping.Group;
import xquerysearch.domain.result.Result;
import xquerysearch.domain.result.ResultSet;

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
	 * Transforms {@link ResultSet} to response-friendly form represented as
	 * String.
	 * 
	 * @param resultSet
	 * @return transformed ResultSet
	 */
	public static String transformResultSet(ResultSet resultSet, long queryTime, long docCount, long arCount) {
		StringBuffer ret = new StringBuffer();
		appendHeaderOfSearch(ret, queryTime, docCount, arCount);

		ret.append("<Hits>");
		if (resultSet == null || resultSet.getResults() == null) {
			ret.append("");
		} else {
			for (Result result : resultSet.getResults()) {
				ret.append(transformResult(result));
			}
		}
		ret.append("</Hits></SearchResult>");
		return ret.toString();
	}

	/**
	 * TODO documentation
	 * 
	 * @param groups
	 * @param queryTime
	 * @param docCount
	 * @param arCount
	 * @return
	 */
	public static String transformResultGroups(List<Group> groups, long queryTime, long docCount, long arCount) {
		StringBuffer ret = new StringBuffer();
		appendHeaderOfSearch(ret, queryTime, docCount, arCount);
		ret.append("<Hits>");
		if (groups == null) {
			ret.append("");
		} else {

			for (Group group : groups) {
				ret.append(group.toString());
			}

		}
		ret.append("</Hits></SearchResult>");
		return ret.toString();
	}

	/**
	 * Transforms {@link Result} to response-friendly form represented as
	 * String.
	 * 
	 * @param result
	 * @return transformed Result
	 */
	public static String transformResult(Result result) {
		if (result == null) {
			return "";
		}
		return result.toString();
	}

	public static String transformResultsInList(List<Result> results, long queryTime, long docCount,
			long arCount) {
		if (results == null) {
			return null;
		}

		StringBuffer ret = new StringBuffer();
		appendHeaderOfSearch(ret, queryTime, docCount, arCount);

		ret.append("<Hits>");
		for (Result result : results) {
			ret.append(transformResult(result));
		}
		ret.append("</Hits></SearchResult>");
		return ret.toString();
	}
	
	public static String transformResultClusters(List<Cluster> groups, long queryTime, long docCount, long arCount) {
		
		return null;
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
