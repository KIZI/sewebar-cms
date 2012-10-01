package xquerysearch.transformation;

import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.List;

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
	 * Transforms {@link ResultSet} to response-friendly form represented as
	 * String.
	 * 
	 * @param resultSet
	 * @return transformed ResultSet
	 */
	public static String transformResultSet(ResultSet resultSet) {
		if (resultSet == null || resultSet.getResults() == null) {
			return null;
		}

		StringBuffer ret = new StringBuffer();
		// TODO remove zeros - give actual values
		appendHeaderOfSearch(ret, 0, 0, 0);

		ret.append("<Hits>");
		for (Result result : resultSet.getResults()) {
			ret.append(transformResult(result));
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
		return result.toString();
	}

	public static String transformResultsInList(List<Result> results, long queryTime, long docCount, long arCount) {
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

	/**
	 * Creates header for search result output.
	 * TODO better output handling (for whole SearchResult element)
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
