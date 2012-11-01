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
		int id = 0;

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
				antecedentId = Integer.toString(++id);

				id += convertDbaList(antecedent.getDbas(), dbas, bbas, id).size();
			}
			if (consequent != null) {
				consequentId = Integer.toString(++id);

				id += convertDbaList(consequent.getDbas(), dbas, bbas, id).size();
			}
			if (condition != null) {
				conditionId = Integer.toString(++id);

				id += convertDbaList(condition.getDbas(), dbas, bbas, id).size();
			}

			hit.setAssociationRule(ResultForOutputObjectConverter.convert(rule, antecedentId, consequentId,
					conditionId));
		}
		hits.add(hit);
	}

	private static List<String> convertDbaList(List<xquerysearch.domain.result.DBA> dbasResult, List<DBA> dbas, List<BBA> bbas, int id) {
		List<String> localBaRefs = new ArrayList<String>();

		if (dbasResult == null || dbas == null) {
			return localBaRefs;
		}

		for (xquerysearch.domain.result.DBA dba : dbasResult) {
			int dbaId = id++;

			List<String> loopBaRefs = new ArrayList<String>();

			if (dba.getDbas() != null && dba.getDbas().size() > 0) {
				loopBaRefs = convertDbaList(dba.getDbas(), dbas, bbas, dbaId);
				dbaId += loopBaRefs.size();
			} else if (dba.getBbas() != null && dba.getBbas().size() > 0) {
				loopBaRefs = convertBbaList(bbas, dba.getBbas(), dbaId);
				dbaId += loopBaRefs.size();
			}

			DBA dbaOut = ResultForOutputObjectConverter.convert(dba.getConnective(), Integer.toString(dbaId),
					loopBaRefs);

			dbas.add(dbaOut);
			localBaRefs.add(Integer.toString(dbaId));
		}

		return localBaRefs;
	}

	private static List<String> convertBbaList(List<BBA> bbas, List<xquerysearch.domain.result.BBA> bbasResult, int id) {
		List<String> localBaRefs = new ArrayList<String>();

		for (xquerysearch.domain.result.BBA resultBba : bbasResult) {
			String localId = Integer.toString(++id);
			BBA bbaOut = ResultForOutputObjectConverter.convert(resultBba, localId);
			bbaOut.setId(localId);
			bbas.add(bbaOut);
			localBaRefs.add(localId);
		}

		return localBaRefs;
	}
}
