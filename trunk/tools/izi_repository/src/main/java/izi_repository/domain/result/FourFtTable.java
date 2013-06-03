package izi_repository.domain.result;

/**
 * Domain object representing FourFtTable element from query result.
 * 
 * @author Tomas Marek
 * 
 */
public class FourFtTable {

	private int a;
	private int b;
	private int c;
	private int d;

	/**
	 * @return the a
	 */
	public int getA() {
		return a;
	}

	/**
	 * @param a
	 *            the a to set
	 */
	public void setA(int a) {
		this.a = a;
	}

	/**
	 * @return the b
	 */
	public int getB() {
		return b;
	}

	/**
	 * @param b
	 *            the b to set
	 */
	public void setB(int b) {
		this.b = b;
	}

	/**
	 * @return the c
	 */
	public int getC() {
		return c;
	}

	/**
	 * @param c
	 *            the c to set
	 */
	public void setC(int c) {
		this.c = c;
	}

	/**
	 * @return the d
	 */
	public int getD() {
		return d;
	}

	/**
	 * @param d
	 *            the d to set
	 */
	public void setD(int d) {
		this.d = d;
	}
	
	/**
	 * @{inheritDoc}
	 */
	@Override
	public String toString() {
		return "<FourFtTable a=\"" + a + "\" b=\"" + b + "\" c=\"" + c + "\" d=\"" + d + "\" />";
	}

}
