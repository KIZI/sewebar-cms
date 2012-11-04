package xquerysearch.outputconverting;

import java.util.ArrayList;
import java.util.List;

import xquerysearch.domain.output.BBA;
import xquerysearch.domain.output.DBA;
import xquerysearch.domain.output.Hit;
import xquerysearch.domain.result.Cedent;
import xquerysearch.domain.result.Result;
import xquerysearch.domain.result.Rule;
import xquerysearch.transformation.ResultForOutputObjectConverter;

/**
 * Converter for result objects to output objects.
 * 
 * @author Tomas Marek
 * 
 */
public class OutputConverter {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private OutputConverter() {
	}

	/**
	 * Fills given lists with data from given list of {@link Result}.
	 * 
	 * @param bbas
	 * @param dbas
	 * @param hits
	 * @param results
	 */
	public static void convertResult(List<BBA> bbas, List<DBA> dbas, List<Hit> hits, Result result) {
		Integer id = 0;

		Hit hit = ResultForOutputObjectConverter.convert(result);

		Rule rule = result.getRule();

		if (rule != null) {
			String antecedentId = null;
			String consequentId = null;
			String conditionId = null;

			Cedent antecedent = rule.getAntecedent();
			Cedent consequent = rule.getConsequent();
			Cedent condition = rule.getCondition();

			if (antecedent != null) {
				id++;
				antecedentId = id.toString();

				convertDbaListFirstLevel(antecedent.getDbas(), dbas, bbas, id);
			}
			if (consequent != null) {
				id++;
				consequentId = id.toString();

				convertDbaListFirstLevel(consequent.getDbas(), dbas, bbas, id);
			}
			if (condition != null) {
				id++;
				conditionId = id.toString();

				convertDbaListFirstLevel(condition.getDbas(), dbas, bbas, id);
			}

			hit.setAssociationRule(ResultForOutputObjectConverter.convert(rule, antecedentId, consequentId,
					conditionId));
		}
		hits.add(hit);
	}

	private static void convertDbaListFirstLevel(List<xquerysearch.domain.result.DBA> dbasResult, List<DBA> dbas, List<BBA> bbas, Integer id) {
		List<String> baRefs = convertDbaList(dbasResult, dbas, bbas, id);
		dbas.add(ResultForOutputObjectConverter.convert("Conjunction", id.toString(), baRefs));
	}

	private static List<String> convertDbaList(List<xquerysearch.domain.result.DBA> dbasResult, List<DBA> dbas, List<BBA> bbas, Integer id) {
		List<String> localBaRefs = new ArrayList<String>();

		if (dbasResult == null || dbas == null) {
			return localBaRefs;
		}
		
		for (xquerysearch.domain.result.DBA dba : dbasResult) {
			id++;
			List<String> loopBaRefs = new ArrayList<String>();

			if (dba.getDbas() != null && dba.getDbas().size() > 0) {
				loopBaRefs = convertDbaList(dba.getDbas(), dbas, bbas, id);
				id += loopBaRefs.size();
			} else if (dba.getBbas() != null && dba.getBbas().size() > 0) {
				loopBaRefs = convertBbaList(bbas, dba.getBbas(), id);
				id += loopBaRefs.size();
			}

			DBA dbaOut = ResultForOutputObjectConverter.convert(dba.getConnective(), id.toString(),
					loopBaRefs);

			dbas.add(dbaOut);
			localBaRefs.add(id.toString());
		}

		return localBaRefs;
	}

	private static List<String> convertBbaList(List<BBA> bbas, List<xquerysearch.domain.result.BBA> bbasResult, Integer id) {
		List<String> localBaRefs = new ArrayList<String>();

		for (xquerysearch.domain.result.BBA resultBba : bbasResult) {
			BBA bbaOut = ResultForOutputObjectConverter.convert(resultBba, id.toString());
			bbaOut.setId(id.toString());
			bbas.add(bbaOut);
			localBaRefs.add(id.toString());
		}

		return localBaRefs;
	}
}
