import { BsArrowRight } from "react-icons/bs"

const Recurring = () => {
  return (
    <div>
      <div className="flex items-center justify-between">
        <h1>Recurring Buy</h1>
        <div>My plans <BsArrowRight/></div>
      </div>
      <div>
        <div>
          <h1>Create Recurring Plan </h1>
          <div>
            <h3>Select Assets</h3>
            <div>
              <div>
                <span>USD</span>
                <span>{">"}</span>
              </div>
              <span>To</span>
              <div>
                <div>
                  <span>USDT</span>
                  <span>{">"}</span>
                </div>
              </div>
            </div>
            <div>
              <h4>Amount</h4>
              <div>
                <input type="text" placeholder="15 - 15.000"/>
                <span>USD</span>
              </div>
            </div>
          </div>
        </div>
        <div>
          <h1>Top Cryptos</h1>
          
        </div>
      </div>
    </div>
  )
}

export default Recurring