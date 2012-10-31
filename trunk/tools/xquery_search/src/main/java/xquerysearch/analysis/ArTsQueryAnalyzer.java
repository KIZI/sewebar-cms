package xquerysearch.analysis;

import xquerysearch.domain.ArTsQueryInternal;
import xquerysearch.domain.analysis.ArTsQueryAnalysisOutput;
import xquerysearch.domain.arbquery.tasksetting.ArTsBuilderQuery;

/**
 * Analyzer for {@link ArTsQueryInternal}.
 * 
 * @author Tomas Marek
 *
 */
public class ArTsQueryAnalyzer {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	public ArTsQueryAnalyzer() {
	}
	
	/**
	 * Analyzes {@link ArTsBuilderQuery}.
	 * 
	 * @param atqi
	 * @return {@link ArTsQueryAnalysisOutput} describing {@link ArTsBuilderQuery}
	 */
	public static ArTsQueryAnalysisOutput analyze(ArTsQueryInternal atqi) {
		ArTsQueryAnalysisOutput output = new ArTsQueryAnalysisOutput();

		output.setAntecedentBbaCount(atqi.getAntecedentBbaSettings().size());
		output.setConsequentBbaCount(atqi.getConsequentBbaSettings().size());
		output.setConditionBbaCount(atqi.getConditionBbaSettings().size());

		return output;
	}
}
