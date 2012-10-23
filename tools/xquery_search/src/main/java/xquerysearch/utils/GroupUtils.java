package xquerysearch.utils;

import java.util.List;

import xquerysearch.domain.grouping.Group;
import xquerysearch.domain.grouping.GroupDescription;

/**
 * Class providing helping methods for using {@link Group}s.
 * 
 * @author Tomas Marek
 * 
 */
public class GroupUtils {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private GroupUtils() {
	}

	/**
	 * TODO documentation
	 * 
	 * @param groups
	 * @param categoryNames
	 * @return
	 */
	public static Group getGroupByCategories(List<Group> groups, List<String> categoryNames, String fieldRef) {
		if (groups == null || categoryNames == null) {
			return null;
		}

		for (Group group : groups) {
			if (isSuitableGroupByCategories(group, categoryNames, fieldRef)) {
				return group;
			}
		}

		return null;
	}

	/**
	 * TODO documentation
	 * 
	 * @param groups
	 * @param fieldRef
	 * @return
	 */
	public static Group getGroupByFieldRef(List<Group> groups, List<String> fieldRefs) {
		if (groups == null || fieldRefs == null) {
			return null;
		}

		for (Group group : groups) {
			GroupDescription description = group.getDescription();
			if (description != null && isSuitableGroupByFieldRefs(description.getFieldRefs(), fieldRefs)) {
				return group;
			}
		}
		return null;
	}

	/**
	 * TODO documentation
	 * 
	 * @param groups
	 * @param ruleLength
	 * @return
	 */
	public static Group getGroupByRuleLength(List<Group> groups, int ruleLength) {
		if (groups == null) {
			return null;
		}

		for (Group group : groups) {
			if (group.getDescription() != null && group.getDescription().getRuleLength() != null
					&& group.getDescription().getRuleLength() == ruleLength) {
				return group;
			}
		}

		return null;
	}

	/**
	 * TODO documentation
	 * 
	 * @param groups
	 * @param antecedentLength
	 * @param consequentLength
	 * @param conditionLength
	 * @return
	 */
	public static Group getGroupByCedentsLength(List<Group> groups, int antecedentLength, int consequentLength, int conditionLength) {
		if (groups == null) {
			return null;
		}

		for (Group group : groups) {
			if (isSuitableGroupByCedentsLength(group, antecedentLength, consequentLength, conditionLength)) {
				return group;
			}
		}

		return null;
	}

	/**
	 * TODO documentation
	 * 
	 * @param groups
	 * @param antecedentFieldRefs
	 * @param consequentFieldRefs
	 * @param conditionFieldRefs
	 * @return
	 */
	public static Group getGroupByCedentFieldRef(List<Group> groups, List<String> antecedentFieldRefs, List<String> consequentFieldRefs, List<String> conditionFieldRefs) {
		if (groups == null || antecedentFieldRefs == null || consequentFieldRefs == null
				|| conditionFieldRefs == null) {
			return null;
		}

		for (Group group : groups) {
			GroupDescription description = group.getDescription();
			if (description != null) {
				boolean isAntecedentSuitable = isSuitableGroupByFieldRefs(description.getAntecedentFieldRefs(), antecedentFieldRefs);
				boolean isConsequentSuitable = isSuitableGroupByFieldRefs(description.getConsequentFieldRefs(), consequentFieldRefs);
				boolean isConditionSuitable = isSuitableGroupByFieldRefs(description.getConditionFieldRefs(), conditionFieldRefs);
				
				if (isAntecedentSuitable == true && isConsequentSuitable == true && isConditionSuitable == true) {
					return group;
				}
			}
		}

		return null;
	}

	/**
	 * TODO documentation
	 * 
	 * @param group
	 * @param categoryNames
	 * @return
	 */
	private static boolean isSuitableGroupByCategories(Group group, List<String> categoryNames, String fieldRef) {
		if (group == null || categoryNames == null) {
			return false;
		}

		GroupDescription description = group.getDescription();
		if (description == null) {
			return false;
		}
		if (description.getFieldRefName().equals(fieldRef) == false) {
			return false;
		}
		boolean isSuitable = true;
		for (String categoryName : categoryNames) {
			if (description.getCategories().contains(categoryName) == false) {
				isSuitable = false;
			}
		}
		if (isSuitable != false && (description.getCategories().size() != categoryNames.size())) {
			isSuitable = false;
		}

		return isSuitable;
	}

	/**
	 * TODO documentation
	 * 
	 * @param antecedentLength
	 * @param consequentLength
	 * @param conditionLength
	 * @return
	 */
	private static boolean isSuitableGroupByCedentsLength(Group group, int antecedentLength, int consequentLength, int conditionLength) {
		if (group.getDescription() == null || group.getDescription().getAntecedentLength() == null
				|| group.getDescription().getConsequentLength() == null
				|| group.getDescription().getConditionLength() == null) {
			return false;
		} else {
			if (group.getDescription().getAntecedentLength() == antecedentLength
					&& group.getDescription().getConsequentLength() == consequentLength
					&& group.getDescription().getConditionLength() == conditionLength) {
				return true;
			}
		}
		return false;
	}

	/**
	 * TODO documentation
	 * 
	 * @param groupFieldRefs
	 * @param fieldRefs
	 * @return
	 */
	private static boolean isSuitableGroupByFieldRefs(List<String> groupFieldRefs, List<String> fieldRefs) {
		if (groupFieldRefs == null || fieldRefs == null) {
			return false;
		}

		boolean isSuitable = true;
		for (String fieldRef : fieldRefs) {
			if (groupFieldRefs.contains(fieldRef) == false) {
				isSuitable = false;
			}
		}
		if (isSuitable != false && (groupFieldRefs.size() != fieldRefs.size())) {
			return false;
		}
		return isSuitable;
	}
}
