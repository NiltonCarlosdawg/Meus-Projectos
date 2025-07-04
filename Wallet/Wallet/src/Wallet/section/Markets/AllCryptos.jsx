import { useEffect, useState } from "react"
import { allCoins } from "./Object"
import { PiCoinFill } from "react-icons/pi"
import axios from "axios"
const AllCryptos = ({search}) => {
  const [searchData, setSearchData] = useState([])
  const currency = (amount)=>{
    if(String(amount).includes(",")){
      amount = Number(amount).replace(",", ".")
    }
    const pamount = amount
    const decimals = 2
    const valorFormatado = pamount.toLocaleString("USA", {
      style:"currency",
      currency:"usd",
      min:decimals,
      max:decimals
    })
    return valorFormatado
  }
  useEffect(()=>{
    let filter = allCoins
    if(filter !== ""){
      filter = filter.filter(item=>(item.name.toLowerCase().includes(search.toLowerCase())))
      setSearchData(filter)
    }
    
  }, [search])
  const [cryptos, setCryptos] = useState([]);

  const getCryptos = async () => {
    try {
      // Chamada para o backend (localhost)
      const response = await axios.get('http://localhost:5000/api/cryptos');
      setCryptos(response.data);
    } catch (error) {
      console.error('Erro ao buscar dados do backend:', error.message);
    }
  };

  useEffect(() => {
    getCryptos();
  }, []);
  return (
    <div className="w-full rounded-md">
      <table className="w-full shadow-lg rounded-md">
        <thead className="bg-[#0000001d] border-b-[1px] border-[#8080808d] rounded-t-[5px]">
          <tr className="text-[#888da8] text-[14px]">
            <th className="py-[5px]">Nome</th>
            <th>Preço</th>
            <th>Variação/Alterar</th>
            <th>Volume em 24h</th>
            <th>Cap de mercado</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          {
            searchData.map((item, index)=>{
              return(
                <tr key={index} className="border-b-[1px] border-[#80808056] text-center cursor-pointer">
                  <td className="py-[5px] flex items-center gap-1 justify-center"><PiCoinFill size={20} color="#f5cf34"/>{item.name}</td>
                  <td className="font-[500]">{currency(item.price)}</td>
                  <td className={item.variation> 0?"text-[#299f29] font-bold":"text-[red] font-bold"}>{item.variation}%</td>
                  <td className="font-[500]">${item.volumeBTC}B</td>
                  <td className="font-[500]">${item.cap}B</td>
                </tr>
              )
            })
          }
        </tbody>
      </table>
    </div>
  )
}

export default AllCryptos