package xquerysearch.analysis;

import xquerysearch.domain.ArQueryInternal;
import xquerysearch.domain.analysis.ArQueryAnalysisOutput;
import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.arbquery.ArQuery;

/**
 * Analyzer for {@link ArQuery}.
 * 
 * @author Tomas Marek
 * 
 */
public class ArQueryAnalyzer {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private ArQueryAnalyzer() {
	}

	/**
	 * Analyzes {@link ArBuilderQuery}.
	 * 
	 * @param aqi
	 * @return {@link ArQueryAnalysisOutput} describing {@link ArBuilderQuery}
	 */
	public static ArQueryAnalysisOutput analyze(ArQueryInternal aqi) {
		ArQueryAnalysisOutput output = new ArQueryAnalysisOutput();

		output.setAntecedentBbaCount(aqi.getAntecedentBbaSettingList().size());
		output.setConsequentBbaCount(aqi.getConsequentBbaSettingList().size());
		output.setConditionBbaCount(aqi.getConditionBbaSettingList().size());

		return output;
	}

}
